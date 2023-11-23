import { FC } from 'react';
import styled from 'styled-components';
import { BookingCard } from './BookingCard';
import { useDrop } from 'react-dnd';
import { ItemTypes } from './general';
import { DraggableBooking } from './DraggableBooking';

export interface WishlistProps {
  wishlistEvents: Array<any>;
}
const StyledWishList = styled.div`
  .wishlistContainer {
    max-height: 30rem;
    overflow: auto;
  }
`;
export const Wishlist: FC<WishlistProps> = ({ wishlistEvents }) => {
  // const [{ isOver, canDrop }, drop] = useDrop(() => ({
  //   accept: ItemTypes.LESSON,
  //   collect: (monitor) => ({
  //     isOver: !!monitor.isOver(),
  //     canDrop: !!monitor.canDrop(),
  //   }),
  //   drop: (item, monitor) => {
  //     console.log(item, monitor, 'On DROP');
  //     return undefined;
  //   },
  // }));
  return (
    <StyledWishList>
      <h3>Wishlist</h3>
      <div className="wishlistContainer">
        {wishlistEvents.map((elm, index) => (
          <DraggableBooking
            event={elm}
            key={`wl-${index}`}
            status="wishlist"
          ></DraggableBooking>
        ))}
      </div>
    </StyledWishList>
  );
};
